import os
import re

def update_categories(root_dir):
    for dirpath, _, filenames in os.walk(root_dir):
        for filename in filenames:
            if filename.endswith(".txt"):
                filepath = os.path.join(dirpath, filename)
                with open(filepath, "r", encoding="utf-8") as f:
                    lines = f.readlines()

                modified = False
                new_lines = []
                for line in lines:
                    if line.startswith("Child-category-selector:"):
                        original_value = line.split(":", 1)[1].strip()
                        if not original_value:
                            new_lines.append(line)
                            continue

                        categories = [c.strip() for c in original_value.split(",")]
                        new_categories = []
                        line_modified = False
                        for cat in categories:
                            if "Dipartimento" in cat:
                                new_cat = cat.replace("Dipartimento ", "").strip()
                                new_categories.append(new_cat)
                                line_modified = True
                            else:
                                new_categories.append(cat)
                        
                        if line_modified:
                            new_line = "Child-category-selector: " + ", ".join(new_categories) + "\n"
                            new_lines.append(new_line)
                            modified = True
                            print(f"Updated {filepath}: '{original_value}' -> '{', '.join(new_categories)}'")
                        else:
                            new_lines.append(line)
                    else:
                        new_lines.append(line)

                if modified:
                    with open(filepath, "w", encoding="utf-8") as f:
                        f.writelines(new_lines)

if __name__ == "__main__":
    content_dir = "/Users/ff3300/Desktop/SITI/k5-spicgil/content"
    update_categories(content_dir)
