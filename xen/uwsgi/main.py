#!/usr/bin/env python 
import web 
import os
import json
from urllib import unquote

urls = ("/.*", "main")

class main:
    def GET(self):
        try:
            query = unquote(web.ctx.env['QUERY_STRING'])
            info = query.split('&')
            if len(info) < 1 :
                return ''
            module = info[0].split('=')
            if len(module) < 1 :
                return ''
            module = module[1]
            if module.startswith('ecloud_'):
                xmodule = __import__(module)
                return xmodule.invoke(query)
            else:
                return "Not Found Module %s"%(module)
        except ImportError as e:
            return e
        except AttributeError as e:
            return e
        except Exception as e:
            return e

    def POST(self):
        try:
            post_data = json.loads(web.data())
            module = post_data['module']
            if module.startswith('ecloud_'):
                xmodule = __import__(module)
                return xmodule.invoke(web.data())
            else:
                return "Not Found Module %s"%(module)
        except ImportError as e:
            return e
        except AttributeError as e:
            return e
        except Exception as e:
            return e
 
app = web.application(urls, globals())

application = app.wsgifunc()

