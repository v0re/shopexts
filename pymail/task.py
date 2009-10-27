import sqlite3


#cx = sqlite3.connect("task.db")
#cu = cx.cursor()


#cu.execute(
#"""
#CREATE TABLE task(
#	id INTEGER PRIMARY KEY,
#	title VARCHAR(200),
#	content TEXT,
#	addtime INTEGER,
#	updatetime INTEGER
#)
#"""
#)




class task:
	

	def __init__(self):
		self.cx = sqlite3.connect('task.db')
		self.cu = self.cx.cursor()
			

	def getList(self):
		sql = 'SELECT * FROM task'
		self.cu.execute(sql)
		rows = self.cu.fetchall()	
		print rows
		return rows
		

instance = task()
tasks = instance.getList()
print tasks