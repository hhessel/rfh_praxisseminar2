<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

if($userHandler->login()->isLoggedIn()) {
	$templater->loadTemplate('kurse.html');
	$currentUser = $userHandler->getCurrentUser();
	$templater->showWelcome($currentUser);
} else {
	$data = array ('error' => 'Du bist nicht eingeloggt<br\>', 'redirect' => 'kurse');
	$templater->loadTemplate('login.html')->data($data);
}

$course_not_found_error = array ('error' => 'Kurs nicht gefunden<br\>', 'redirect' => 'kurse');
$attachment_not_found_error = array ('error' => 'Keine Notizen<br\>', 'redirect' => 'kurse');

if(isset($_GET['add'])) {
	$templater->loadTemplate('attachment_add.html')->data(array('kursId'=>(int)$_GET['add']));
}

if(isset($_GET['delete_attachment'])) {
	if($userHandler->isAdmin()) {
		$db->model('attachment')
			->delete()
			->where('id', (int)$_GET['delete_attachment'])
			->execute();
	}
	header("Location: kurse.php");
	exit();
}

if(isset($_FILES['upload']) && isset($_POST['text'])) {
	$templater->loadTemplate('kurse.html');
	move_uploaded_file($_FILES['upload']['tmp_name'], "upload/".$_FILES['upload']['name']);
	
	$db->model('attachment')->insert(
		array(	'kursId' => (int)$_POST['kursId'],
				'fromUser' => $currentUser['id'],
				'text' => $_POST['text'],
				'filename' => $_FILES['upload']['name']
			)
		)->execute();
		
	$_GET['view'] = $_POST['kursId'];
}

if(isset($_GET['view'])) {
	$count = $db->model('kurse')->count()->where("id", (int)$_GET['view'])->execute()->result;
	
	if($count > 0) {
		$attachments = $db->model('attachment')
						->select('*')
						->where('kursId', (int)$_GET['view'])
						->execute()
						->result;
		
		if(count($attachments) > 0) {
			$attachments_uebersicht = '<table style="width:700px;"><tr><td style="width:150px; text-align:right;">Von</td><td style="width:250px;">Notiz</td><td width="*">Download</td><td></td></tr>';
			
			while (list(, $attachment) = each($attachments)) {
			
				$fromUser = $db->model('user')
						->select('*')
						->where('id', (int)$attachment['fromUser'])
						->execute()
						->result[0];
			
				$attachment_uebersicht = '<tr>
										<td style="text-align:right;">' .  $fromUser['firstname'] . ' ' . $fromUser['lastname'] . '</td>
										<td>' . $attachment['text'] . '</td>
										<td style="vertical-align:middle;"><a target="_blank" href=upload/' . $attachment['filename'] . '>' . $attachment['filename'].'</a></td>';
										
				$attachment_uebersicht .= $userHandler->isAdmin() ? '<td><a href="kurse_view.php?delete_attachment=' . $attachment['id'] .'"> Delete</a></td>' : '<td></td>';
				
				$attachment_uebersicht .= '</tr>';				
				$attachments_uebersicht .= $attachment_uebersicht;
				
			}
		
			$attachments_uebersicht .= '</table>';
			$templater->data(array('kurs_attachments' => $attachments_uebersicht));
			
		} else $templater->data($attachment_not_found_error);
								
	} else $templater->data($course_not_found_error);
} 

echo $templater->show();