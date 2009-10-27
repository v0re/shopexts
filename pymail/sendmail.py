import time,datetime
import threading

def worker(a_tid, a_account):
	global g_mutex
	print "Str ", a_tid, datetime.datetime.now()
	for i in range( 1000000 ):
		#get lock
		g_mutex.acquire()
		#modify resource
		a_account.depostie(1)
		#release lock
		g_mutex.release()
	print "End ", a_tid, datetime.datetime.now()

class Account:
	def __init__(self, a_base):
		self.m_amount = a_base
	
	def depostie(self, a_amount):
		self.m_amount += a_amount

	def withdraw(self, a_amount):
		self.m_amount -= a_amount

if __name__ == "__main__":
	global g_mutex
	count = 0
	dstart = datetime.datetime.now()
	print "Main thread start at: ", dstart

	#init thread_pool
	thread_pool = []
	#init mutex
	g_mutex = threading.Lock()
	#init thread items
	acc = Account(100)
	for i in range(10):
		th = threading.Thread(target = worker, args = (i, acc))
		thread_pool.append(th)
	#start threads one by one
	for i in range(10):
		thread_pool[i].start()
	#collect all threads
	for i in range(10):
		threading.Thread.join(thread_pool[i])
	dend = datetime.datetime.now()
	print "count= ", acc.m_amount
	print "Main thread end at: ", dend, " time span ", dend - dstart