#!/bin/bash

PLIK_JAVA=$1
MANIFEST=$2
PLIK_JAR='JavaPhP.jar'


javac -Djava.ext.dirs=. $PLIK_JAVA.java
jar cvmf $MANIFEST $PLIK_JAR $PLIK_JAVA.class

