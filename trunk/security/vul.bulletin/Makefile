t2t_bin=bin/txt2tags.exe
svn=D:/Shell/bin/svn.exe
out_type=--target=html
htc_dir=htdocs
t2t_dir=t2t
t2t_files=$(wildcard $(t2t_dir)/*.t2t)
html_files=$(t2t_files:$(t2t_dir)/%.t2t=$(htc_dir)/%.html)

all:$(html_files)
	
$(htc_dir)/%.html:$(t2t_dir)/%.t2t
	${t2t_bin} ${out_type} --infile=$< --outfile=$@ 

.PHONY:update
update:
	$(svn) add $(htc_dir)
	$(svn) add $(t2t_dir)
	${svn} commit -m 'update vul ok'

.PHONY:clean    
clean:
	rm -f  *.html
	rm -f htdocs/*.html

