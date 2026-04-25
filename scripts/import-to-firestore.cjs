const { initializeApp, cert } = require('firebase-admin/app');
const { getFirestore, Timestamp } = require('firebase-admin/firestore');
const fs = require('fs');
const path = require('path');

/**
 * Complete PostgreSQL -> Firestore Import
 * Clears existing collections and imports all data fresh.
 */

const SERVICE_ACCOUNT_PATH = process.env.GOOGLE_APPLICATION_CREDENTIALS
    || path.join(__dirname, '..', 'storage', 'app', 'firebase-service-account.json');

const EXPORT_DIR = path.join(__dirname, '..', 'database', 'firestore_export');

const COLLECTION_MAP = {
    'users': 'users',
    'phones': 'phones',
    'benchmarks': 'benchmarks',
    'spec_batteries': 'spec_batteries',
    'spec_bodies': 'spec_bodies',
    'spec_cameras': 'spec_cameras',
    'spec_connectivities': 'spec_connectivities',
    'spec_platforms': 'spec_platforms',
    'comments': 'comments',
    'comment_upvotes': 'comment_upvotes',
    'blogs': 'blogs',
    'forum_categories': 'forum_categories',
    'forum_posts': 'forum_posts',
    'forum_comments': 'forum_comments',
    'chats': 'chats',
    'chat_messages': 'chat_messages',
};

const SKIP_TABLES = [
    'cache', 'cache_locks', 'failed_jobs', 'job_batches', 'jobs',
    'migrations', 'password_reset_tokens', 'personal_access_tokens', 'sessions'
];

function convertValue(key, value) {
    if (value === null || value === undefined) return null;
    if (typeof value === 'boolean') return value;
    if (typeof value === 'number') return value;
    if (value instanceof Date) return Timestamp.fromDate(value);

    if (typeof value === 'string') {
        // Try to parse ISO timestamps
        const isoPattern = /^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})?$/;
        if (isoPattern.test(value)) {
            const d = new Date(value);
            if (!isNaN(d.getTime())) {
                return Timestamp.fromDate(d);
            }
        }
        return value;
    }

    if (Array.isArray(value)) {
        return value.map(v => convertValue('', v));
    }

    if (typeof value === 'object') {
        const result = {};
        for (const [k, v] of Object.entries(value)) {
            result[k] = convertValue(k, v);
        }
        return result;
    }

    return value;
}

async function clearCollection(db, collectionName) {
    const batchSize = 500;
    const collectionRef = db.collection(collectionName);
    const query = collectionRef.limit(batchSize);

    return new Promise((resolve, reject) => {
        deleteQueryBatch(db, query, resolve).catch(reject);
    });
}

async function deleteQueryBatch(db, query, resolve) {
    const snapshot = await query.get();
    const batchSize = snapshot.size;
    if (batchSize === 0) {
        resolve();
        return;
    }

    const batch = db.batch();
    snapshot.docs.forEach((doc) => {
        batch.delete(doc.ref);
    });

    await batch.commit();
    process.nextTick(() => {
        deleteQueryBatch(db, query, resolve);
    });
}

async function main() {
    if (!fs.existsSync(SERVICE_ACCOUNT_PATH)) {
        console.error(`Service account not found at: ${SERVICE_ACCOUNT_PATH}`);
        process.exit(1);
    }

    const serviceAccount = require(SERVICE_ACCOUNT_PATH);

    initializeApp({
        credential: cert(serviceAccount)
    });

    const db = getFirestore();

    console.log('=== PhoneFinderHub: PostgreSQL -> Firestore Migration ===\n');

    // Step 1: Clear existing collections
    console.log('Step 1: Clearing existing Firestore collections...');
    for (const collectionName of Object.values(COLLECTION_MAP)) {
        if (SKIP_TABLES.includes(collectionName)) continue;
        await clearCollection(db, collectionName);
        console.log(`  Cleared: ${collectionName}`);
    }

    // Step 2: Import data
    console.log('\nStep 2: Importing data...');
    let totalImported = 0;
    let totalCollections = 0;

    for (const [tableName, collectionName] of Object.entries(COLLECTION_MAP)) {
        if (SKIP_TABLES.includes(tableName)) continue;

        const jsonPath = path.join(EXPORT_DIR, `${tableName}.json`);
        if (!fs.existsSync(jsonPath)) {
            console.log(`  SKIP: ${tableName} (no export file)`);
            continue;
        }

        const documents = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));
        if (!Array.isArray(documents) || documents.length === 0) {
            console.log(`  SKIP: ${tableName} (empty)`);
            continue;
        }

        totalCollections++;
        let imported = 0;
        let batch = db.batch();
        let batchCount = 0;
        const BATCH_LIMIT = 500;

        for (const doc of documents) {
            const docId = String(doc.id || Date.now() + '_' + Math.random().toString(36).substr(2, 9));
            delete doc.id;

            // Convert all values for Firestore compatibility
            const firestoreDoc = {};
            for (const [key, value] of Object.entries(doc)) {
                firestoreDoc[key] = convertValue(key, value);
            }

            const docRef = db.collection(collectionName).doc(docId);
            batch.set(docRef, firestoreDoc);

            batchCount++;
            imported++;

            if (batchCount >= BATCH_LIMIT) {
                await batch.commit();
                batch = db.batch();
                batchCount = 0;
            }
        }

        if (batchCount > 0) {
            await batch.commit();
        }

        totalImported += imported;
        console.log(`  IMPORTED: ${collectionName} -> ${imported} documents`);
    }

    console.log('\n=== Migration Complete ===');
    console.log(`Collections: ${totalCollections}`);
    console.log(`Total documents: ${totalImported}`);
    process.exit(0);
}

main().catch(err => {
    console.error('Migration failed:', err);
    process.exit(1);
});
