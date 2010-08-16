t2t_bin=bin/txt2tags.exe
svn=D:/Shell/bin/svn.exe
out_type=--target=html
htc_dir=htdocs
t2t_dir=t2t
t2t_files=$(wildcard $(t2t_dir)/*.t2t)
html_files=$(t2t_files:$(t2t_dir)/%.t2t=%.html)

all:$(html_files)
	${svn} commit -m 'update vul ok'
$(html_files):$(t2t_files)
	${t2t_bin} ${out_type} --infile=$< --outfile=$(htc_dir)/$@ ; \
	$(svn) add $@

.PHONY:clean    
clean:
	rm -f  *.html
	rm -f htdocs/*.html

