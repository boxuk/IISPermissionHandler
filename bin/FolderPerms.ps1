#
# This snippet gets a dir from ther user, sets owner of thet folder and subfolders to the
# Administrators group and grants the user IUSR Full access recursively.
#
#

#Stop IIS
IISReset /STOP

echo ""
echo "=======Set Folder Perms======="
echo ""
echo ""
echo "Set Administrators as Owner"
echo ""


foreach ($i in $args)
{
    icacls "$i" /setowner administrators /t
}

echo ""
echo "Grant IUSR R\W"
echo ""


foreach ($i in $args)
{
	icacls "$i" /grant:r IUSR:F /t
	icacls "$i" /grant:r IUSR:"(OI)(CI)"F /t
}

#Start IIS
IISReset /START
