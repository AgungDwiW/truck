#!/usr/bin/env python3
"""
Count mysqli_query occurrences in PHP files.
"""
import os
import re

def count_queries_in_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        # Count occurrences of mysqli_query (case-insensitive)
        matches = re.findall(r'mysqli_query\s*\(', content, re.IGNORECASE)
        return len(matches)
    except Exception as e:
        print(f"Error reading {filepath}: {e}")
        return 0

def main():
    directory = r'C:\xampp\htdocs\truck\application\views\main'
    php_files = []
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.lower().endswith('.php'):
                php_files.append(os.path.join(root, file))
    
    print(f"Found {len(php_files)} PHP files.")
    print("=" * 60)
    
    file_counts = []
    for filepath in php_files:
        count = count_queries_in_file(filepath)
        if count > 0:
            file_counts.append((filepath, count))
    
    # Sort by count descending
    file_counts.sort(key=lambda x: x[1], reverse=True)
    
    for filepath, count in file_counts:
        rel_path = os.path.relpath(filepath, directory)
        print(f"{rel_path:30} : {count:2} queries")
    
    print("=" * 60)
    total_queries = sum(c for _, c in file_counts)
    print(f"Total queries: {total_queries}")

if __name__ == '__main__':
    main()