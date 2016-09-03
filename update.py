#!/usr/bin/python
# encoding:utf-8

import urllib
import urllib2
import time
import os
import sys
import logging
import logging.config
import json

reload(sys)
sys.setdefaultencoding('utf8')
os.chdir(sys.path[0])
logging.config.fileConfig("logging_monitor.conf")    # 采用配置文件
logger = logging.getLogger('update')

# 自定义的异常类
class NetworkError(Exception):
	tp = 0
	name = ""
	def __init__(self, arg1, arg2):
		self.tp = arg1
		self.name = arg2

ErrorType = {0:'Database Connection Error',1:'Database Query Error',2:'Database Modification Error',
             3:'File Deletion Error',4:'File Modification Error',5:'IP Conflict Error',6:'Invalid FIB Error',7:'Invalid Info File Error'}

def sendpost(url, data):
	data_urlencode = urllib.urlencode(data)
	req = urllib2.Request(url=url, data=data_urlencode)
	res_data = urllib2.urlopen(req)
	res = res_data.read()
	fail = json.loads(res)
	if fail['Fail'] == 1:
		raise NetworkError(fail['Type'],os.path.basename(url))
	return


while True:
	time.sleep(2)  # time
	try:
		data = ""
		sendpost("http://localhost/readinfo.php", data)
	except NetworkError, e:
		logger.error('In '+str(e.name)+": "+ErrorType[e.tp])
		continue
	except urllib2.HTTPError, e:
		logger.error('HTTP Error[%s]' % str(e.code))
		continue
	except urllib2.URLError, e:
		logger.error('URL Error: '+str(e.reason))
		continue
	except KeyboardInterrupt:
		break
	except Exception,e:
		logger.error('Other Error: '+str(e))
		continue
