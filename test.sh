PREV=""
DELFILE=""
EXECFILE=""
git diff-tree --no-commit-id --name-status -r HEAD
for i in `git diff-tree --no-commit-id --name-status -r HEAD`
do
	if [ "$PREV" = "D" ]
	then
		DELFILE="$DELFILE $i"
	elif [ "$PREV" = "M" ] || [ "$PREV" = "A" ] ; 
	then
		EXECFILE="$EXECFILE $i"
	fi

	PREV=${i}
done

echo ${DELFILE}
echo "EXECFILE"
echo ${EXECFILE}