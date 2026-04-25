# Firebase Migration Guide for PhoneFinderHub

This guide explains how to complete the migration from PostgreSQL (Aiven Cloud) to Firebase (Firestore + Authentication).

## What Has Been Done

1. **Packages Installed**
   - `kreait/laravel-firebase` - Firebase Admin SDK for PHP
   - `laravel/breeze` - Authentication scaffolding (with Firebase Google OAuth integration)
   - `laravel/boost` - AI development tools for Laravel
   - `firebase` (npm) - Firebase JavaScript SDK (already existed)

2. **Database Export**
   - PostgreSQL database dumped to `database_dump.sql`
   - SQL data converted to Firestore-compatible JSON in `database/firestore_export/`

3. **Firebase Auth Setup**
   - `FirebaseAuthService` for token verification and user sync
   - Google Sign-In button added to login page
   - `/auth/firebase/callback` endpoint for Firebase auth
   - `firebase_uid` and `photo_url` columns added to `users` table

4. **Firestore Infrastructure**
   - `FirestoreClient` - REST API client (works without PHP grpc extension)
   - `FirestoreRepository` - Base repository class
   - Specific repositories: `UserRepository`, `PhoneRepository`, `CommentRepository`, `BlogRepository`

5. **Frontend Integration**
   - `resources/js/firebase.js` - Firebase JS SDK initialization
   - Google OAuth popup sign-in flow
   - ID token sent to Laravel backend for verification

## Prerequisites

1. **Create a Firebase Project**
   - Go to [Firebase Console](https://console.firebase.google.com/)
   - Create a new project (e.g., `phone-finder-hub`)
   - Enable **Firestore Database** and **Authentication**

2. **Enable Google Sign-In**
   - In Firebase Console → Authentication → Sign-in method
   - Enable **Google** provider
   - Add your domain to Authorized Domains

3. **Download Service Account**
   - Firebase Console → Project Settings → Service Accounts
   - Click "Generate new private key"
   - Save the JSON file as `storage/app/firebase-service-account.json`

4. **Get Web Config**
   - Firebase Console → Project Settings → General
   - Under "Your apps", click the web app icon (</>)
   - Copy the config values to your `.env` file

## Configuration

Update your `.env` file with real Firebase values:

```env
# Firebase Configuration
FIREBASE_PROJECT=your-project-id
FIREBASE_CREDENTIALS=storage/app/firebase-service-account.json
FIREBASE_DATABASE_URL=https://your-project-id.firebaseio.com
FIREBASE_STORAGE_DEFAULT_BUCKET=your-project-id.appspot.com

# Firebase Web Config (for frontend)
FIREBASE_API_KEY=your-api-key
FIREBASE_AUTH_DOMAIN=your-project-id.firebaseapp.com
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_STORAGE_BUCKET=your-project-id.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your-sender-id
FIREBASE_APP_ID=your-app-id
FIREBASE_MEASUREMENT_ID=your-measurement-id
```

## Import Existing Data to Firestore

### Option 1: Using Node.js Script (Recommended)

Ensure Node.js is available and run:

```bash
node scripts/import-to-firestore.js
```

This requires:
- `GOOGLE_APPLICATION_CREDENTIALS` env var pointing to your service account JSON, OR
- The service account JSON at `storage/app/firebase-service-account.json`

### Option 2: Manual Import

You can import the JSON files from `database/firestore_export/` using the Firebase CLI:

```bash
npm install -g firebase-tools
firebase login
firebase firestore:delete --all-collections
# Then use Firebase Admin SDK or console to import
```

## Migrate Controllers to Firestore

The app currently uses Eloquent ORM with PostgreSQL. To fully migrate to Firestore:

1. **Update Controllers** to use Firestore repositories instead of Eloquent:

```php
use App\Services\Firestore\PhoneRepository;

class PhoneController extends Controller
{
    protected PhoneRepository $phones;

    public function __construct(PhoneRepository $phones)
    {
        $this->phones = $phones;
    }

    public function index()
    {
        $phones = $this->phones->all();
        return view('phones.index', compact('phones'));
    }
}
```

2. **Key Differences from Eloquent:**
   - Firestore returns arrays, not Eloquent models
   - No relationships are automatically loaded
   - Queries are more limited (no joins, no complex where clauses)
   - Document IDs are strings

3. **Authentication Flow:**
   - Users can sign in with email/password (existing Laravel auth)
   - OR sign in with Google via Firebase (new)
   - Firebase users are synced to the local `users` table

## Data Structure in Firestore

Collections created:
- `users` - User accounts (synced from Firebase Auth)
- `phones` - Phone specifications and scores
- `benchmarks` - Performance benchmarks
- `spec_batteries`, `spec_bodies`, `spec_cameras`, `spec_connectivities`, `spec_platforms` - Detailed specs
- `comments`, `comment_upvotes` - User comments
- `blogs` - Blog posts
- `forum_categories`, `forum_posts`, `forum_comments` - Forum content
- `chats`, `chat_messages` - AI chat history

## Testing

After configuration:

```bash
# Clear caches
php artisan config:clear
php artisan cache:clear

# Build frontend
npm run build

# Test the app
php artisan serve
```

Visit `/login` and click "Sign in with Google" to test Firebase Auth.

## Troubleshooting

### "Firebase project not configured"
- Ensure `FIREBASE_CREDENTIALS` points to a valid service account JSON
- Check `config/firebase.php` has correct project configuration

### "grpc extension missing"
- The Firestore REST client (`FirestoreClient.php`) works without grpc
- Only `google/cloud-firestore` package requires grpc

### Google Sign-In fails
- Check browser console for Firebase config errors
- Ensure your domain is in Firebase Authorized Domains
- Verify `FIREBASE_API_KEY` and `FIREBASE_AUTH_DOMAIN` are correct

### Data import fails
- Ensure service account has Firestore Admin permissions
- Check `database/firestore_export/` has the JSON files
- Verify Node.js and `firebase-admin` package are available

## Next Steps

1. Update remaining controllers to use Firestore repositories
2. Implement Firestore security rules
3. Set up Firebase Cloud Functions for background jobs (replacing Laravel queues)
4. Migrate file storage to Firebase Cloud Storage
5. Remove PostgreSQL dependency once fully migrated

## Notes

- The app supports dual auth: existing Laravel email/password + Firebase Google OAuth
- During transition, PostgreSQL can remain as fallback
- Firestore is NoSQL - redesign queries that relied on SQL joins
- Consider using Firebase Cloud Functions for complex aggregations
