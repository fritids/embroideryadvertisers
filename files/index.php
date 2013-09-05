<? 
//session_start();
if ($_GET['session']==1) {
	if( $_GET['file']!=NULL) {
		$file_name = preg_replace( '#[^-\w]#', '', $_GET['file'] ).'.zip';
		$free_file = "{$_SERVER['DOCUMENT_ROOT']}/files/{$_GET['type']}/{$file_name}";
			if( file_exists($free_file)) {
				header( 'Cache-Control: public' );
				header( 'Content-Description: File Transfer' );
				header( "Content-Disposition: attachment; filename={$file_name}" );
				header( 'Content-Type: application/zip' );
				header( 'Content-Transfer-Encoding: binary' );
				readfile( $free_file );
				exit;
			}
	}
	die( "ERROR: invalid file or you don't have permissions to download it." );
} else { 
	echo 'You do not have permission to visit this link.';
		echo "Session: ".$_SESSION['loggedin'];
}
//session_unset();
//session_destroy();
//$_SESSION = array();
?>