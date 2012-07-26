#!/usr/bin/python3.2

import xml.etree.ElementTree as etree
import sys, os, os.path
import subprocess

url = 'http://droppy.jp'

def usage():
  print('./generate_doc.py file.xml')

def parse_setting(treeSetting):
  setting = {}
  setting['method'] = treeSetting.attrib['method']
  setting['route'] = treeSetting.attrib['route']
  setting['public'] = setting['route'].find('public-api') != -1
  setting['resource'] = url + setting['route']
  setting['param'] = []
  for paramTree in treeSetting:
    param = paramTree.attrib
    param['optional'] = (param['optional'] == "true")
    setting['param'].append(param)
  return setting

def get_setting_output(setting):
  output = """
  \subsection*{{{0} {{\\tt {1}}}}}
  Description
  \\begin{{itemize}}
  \item Requires authentification: {2}
  \item HTTP method: {0}
  \item Return format: json, xml
  \end{{itemize}}
  \subsubsection*{{Resource URL}}
  \\url{{{3}}}
  \subsubsection*{{Parameters}}
  \\begin{{table}}[h]
    \\begin{{center}}
      \\begin{{tabular}}{{l l}}
        \hline {4}
      \end{{tabular}}
    \\end{{center}}
  \\end{{table}}
  """.format(setting['method'], 
             setting['route'],
             'no' if setting['public'] else 'yes',
             setting['resource'],
             ''.join(get_parameter_output(param) for param in setting['param'])
    )
  return output

def get_parameter_output(param):
  return """
      \\textbf{{{0}}} & \content{{Description. {1}}}
      \\\\
      {2} & {3}\\\\
      \hline""".format(param['id'].replace('_','\_'),
                       ', '.join((key + ': ' + val) for (key, val) in param.items() \
                                 if key not in {'id', 'optional', 'default'}),
                      'Optional' if param['optional'] else '',
                      'No default value.' if 'default' not in param else \
                      'Defaults to ' + param['default'].replace('_','\_')
    )

def get_main_doc():
  return """\documentclass[11pt,a4paper]{article}
\\usepackage[pdftex]{hyperref}
\\usepackage{listings}
\hypersetup{
  hidelinks
}
\\usepackage{enumerate}
\\usepackage[margin=2cm]{geometry}
\\newcommand{\content}[1]{\\begin{minipage}{10cm}\\vspace{2mm}#1\\vspace{2mm}\end{minipage}}
\setlength{\\tabcolsep}{1cm}
\\renewcommand{\\arraystretch}{1.5}
\\begin{document}"""

def make_and_save_doc(root, input):
  main_document = get_main_doc()
  for setting in root:
    output = get_setting_output(parse_setting(setting))
    main_document += """
      {0}
      \\newpage
      """.format(output)
  main_document += """
\end{document}"""
  output = root.attrib['bundle'] + '_' + input.split('/')[-1].split('.')[0] + '_doc'
  os.mkdir(output)
  os.chdir(output)
  with open(output + '.tex', 'w') as f:
    f.write(main_document)
  subprocess.call(["pdflatex", output + '.tex'])
  for f in os.listdir('.'):
    if f.split('.')[-1] in {"aux", "log", "dvi"}:
      os.remove(f)
  os.chdir('..')

if __name__ == '__main__':
  if len(sys.argv) < 2:
    files = subprocess.check_output('find ../Symfony/src -name "api_settings.xml"', shell=True).decode().split('\n')
  else:
    files = [sys.argv[1]]
  for f in files:
    if os.path.isfile(f):
      tree = etree.parse(f)
      root = tree.getroot()
      make_and_save_doc(root, f)
