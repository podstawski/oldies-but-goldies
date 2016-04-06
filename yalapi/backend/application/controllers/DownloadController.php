<?php

require_once 'RestController.php';

class DownloadController extends RestController
{
    public function getAction()
    {
        try {
            $hash = $this->_getParam('id');
            $file = File::find_by_hash($hash);
            $filepath = APPLICATION_PATH . '/../upload/' . $hash;
            $filename = $file->filename;

            if (is_readable($filepath) && ($handler = fopen($filepath, 'r')))
            {
                $filesize = filesize($filepath);
                $mimetype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filepath);

                header('Content-type: ' . $mimetype);
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-length: ' . $filesize);

                while (!feof($handler)) {
                    echo fread($handler, 2048);
                }
                fclose($handler);
                exit;
            }
            $this->setRestResponseAndExit('File with specified hash does not exist', self::HTTP_NOT_FOUND);
        } catch (\RecordNotFound $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function indexAction()  { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function deleteAction() { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function putAction()    { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
}