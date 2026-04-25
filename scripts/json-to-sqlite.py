import sqlite3
import json
import os
from datetime import datetime

db_path = '/home/abhay/repos/phone_finder/database/database.sqlite'
export_dir = '/home/abhay/repos/phone_finder/database/firestore_export'

conn = sqlite3.connect(db_path)
conn.row_factory = sqlite3.Row
cursor = conn.cursor()

def convert_for_sqlite(value):
    if value is None:
        return None
    if isinstance(value, bool):
        return 1 if value else 0
    if isinstance(value, (list, dict)):
        return json.dumps(value)
    if isinstance(value, datetime):
        return value.isoformat()
    return value

def import_table(table_name, json_file, id_column='id'):
    path = os.path.join(export_dir, json_file)
    if not os.path.exists(path):
        print(f"SKIP: {json_file} not found")
        return 0

    with open(path, 'r') as f:
        rows = json.load(f)

    if not rows:
        print(f"SKIP: {table_name} is empty")
        return 0

    # Get columns from first row
    columns = list(rows[0].keys())
    if id_column in columns:
        columns.remove(id_column)
        columns.insert(0, id_column)

    placeholders = ', '.join(['?' for _ in columns])
    col_names = ', '.join(columns)
    sql = f"INSERT OR REPLACE INTO {table_name} ({col_names}) VALUES ({placeholders})"

    count = 0
    for row in rows:
        values = [convert_for_sqlite(row.get(col)) for col in columns]
        try:
            cursor.execute(sql, values)
            count += 1
        except Exception as e:
            print(f"  ERROR importing into {table_name}: {e}")
            print(f"  Row: {row}")

    conn.commit()
    print(f"IMPORTED: {table_name} -> {count} rows")
    return count

print("=== Importing JSON data into SQLite ===\n")

total = 0
total += import_table('users', 'users.json')
total += import_table('phones', 'phones.json')
total += import_table('benchmarks', 'benchmarks.json')
total += import_table('spec_batteries', 'spec_batteries.json')
total += import_table('spec_bodies', 'spec_bodies.json')
total += import_table('spec_cameras', 'spec_cameras.json')
total += import_table('spec_connectivities', 'spec_connectivities.json')
total += import_table('spec_platforms', 'spec_platforms.json')
total += import_table('comments', 'comments.json')
total += import_table('comment_upvotes', 'comment_upvotes.json')
total += import_table('blogs', 'blogs.json')
total += import_table('forum_categories', 'forum_categories.json')
total += import_table('forum_posts', 'forum_posts.json')
total += import_table('forum_comments', 'forum_comments.json')
total += import_table('chats', 'chats.json')
total += import_table('chat_messages', 'chat_messages.json')

print(f"\n=== Total rows imported: {total} ===")
conn.close()
