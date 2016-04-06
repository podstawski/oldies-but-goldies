<?php

class GN_Upload
{
    public static function upload(array $options, $userID = null)
    {
        if (!isset($options['destination'])) {
            throw new LogicException("Destination must be set");
        }

        if (!isset($options['name'])) {
            throw new LogicException("Form item name must be set");
        }

        $adapter      = new Zend_File_Transfer_Adapter_Http();
        $destination  = $options['destination'];
        $formItemName = $options['name'];
        $result       = array();

        if ($adapter->isUploaded($formItemName))
        {
            $adapter->setDestination($destination, $formItemName);

            if (array_key_exists('validators', $options) && is_array($options['validators'])) {
                foreach ($options['validators'] as $validatorName => $validatorOptions) {
                    $adapter->addValidator($validatorName, false, $validatorOptions, $formItemName);
                }
            } else {
                $adapter->addValidator('Extension', false, 'jpg, png, gif, jpeg, doc, docx, xls, xlsx, txt, pdf, rtf, zip', $formItemName);
            }

            $files = $adapter->getFileInfo($formItemName);

            foreach ($files as $file => $info)
            {
                $filename = $info['name'];
                if ($adapter->isValid($file) && $adapter->receive($file))
                {
                    $hash = sha1($filename . microtime());
                    if (rename($destination . '/' . $filename, $destination . '/' . $hash) === true)
                    {
                        $row = File::create(array(
                            'hash'         => $hash,
                            'filename'     => $filename,
                            'size'         => $info['size'],
                            'created_date' => date('Y-m-d H:i:s'),
                            'downloads'    => 0,
                            'user_id'      => $userID
                        ))->to_array();
                        $row['index'] = preg_replace('/\D+/', '', $file);
                        $result[] = (object) $row;
                        continue;
                    }
                }
                if (file_exists($destination . '/' . $filename)) {
                    unlink($destination . '/' . $filename);
                }
            }
        }
        return $result;
    }
}