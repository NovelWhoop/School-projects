# 4. projekt do predmetu ITY
# autor: Roman Halik
# login: xhalik01

FILE=proj4
ALLFILES = proj4.tex makefile proj4.bib czplain.bst

all:
	latex $(FILE).tex
	bibtex $(FILE)
	latex $(FILE).tex
	latex $(FILE).tex
	dvips -t a4 $(FILE).dvi
	ps2pdf $(FILE).ps