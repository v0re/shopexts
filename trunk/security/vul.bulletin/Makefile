linux_bin=D:/bin
t2t_bin=bin/txt2tags.exe
svn=$(linux_bin)/svn.exe
echo=$(linux_bin)/echo.exe
tr=$(linux_bin)/tr.exe
sed=$(linux_bin)/sed.exe
cp=$(linux_bin)/cp
out_type=--target=html
htc_dir=htdocs
t2t_dir=t2t
t2t_files=$(wildcard $(t2t_dir)/*.t2t)
html_files=$(t2t_files:$(t2t_dir)/%.t2t=$(htc_dir)/%.html)




all:$(html_files) index.t2t
	$(t2t_bin) $(out_type) --infile=index.t2t --outfile=$(htc_dir)/index.html
$(htc_dir)/%.html:$(t2t_dir)/%.t2t
	$(t2t_bin) $(out_type) --infile=$< --outfile=$@  ;\
	$(echo) -n " - [" >> index.txt ;\
	head -n1 $< | $(tr) -d "\r\n" >> index.txt ;\
	$(echo) -n " " >> index.txt ;\
	$(echo) -n $@ | $(sed) 's,htdocs/,,g' >> index.txt ;\
	$(echo) -n "]\n" >> index.txt 
	

index.t2t:
	$(cp) etc/index.t2t ./tmp ;\
	cat tmp index.txt > index.t2t

.PHONY:update
update:
	$(svn) add $(htc_dir)/*
	$(svn) add $(t2t_dir)/*
	${svn} commit -m 'update vul ok'

.PHONY:clean    
clean:
	rm -f  *.html
	rm -f htdocs/*.html
	rm -f index.t*
	rm -f tmp

