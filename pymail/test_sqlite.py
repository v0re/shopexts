import sqlite3

cx = sqlite3.connect("test.db")
cu = cx.cursor()


#cu.execute(
#"""
#CREATE TABLE catalog(
#	id INTEGER PRIMARY KEY,
#	pid INTEGER,
#	name VARCHAR(10) UNIQUE
#)
#"""
#)

cu.execute("INSERT INTO catalog VALUES(null, 0, 'name2')")
cu.execute("INSERT INTO catalog VALUES(null, 1, 'hello')")
cx.commit()

cu.execute("SELECT * FROM catalog")
rows = cu.fetchall()

print rows