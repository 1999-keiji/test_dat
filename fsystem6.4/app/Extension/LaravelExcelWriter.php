<?php

namespace App\Extension;

use Cake\Chronos\Chronos;
use Maatwebsite\Excel\Writers\LaravelExcelWriter as BaseLaravelExcelWriter;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;

class LaravelExcelWriter extends BaseLaravelExcelWriter
{
    /**
     * Export the spreadsheet
     *
     * @param  string $ext
     * @param  array  $headers
     * @throws LaravelExcelException
     */
    public function export($ext = 'xlsx', array $headers = [])
    {
        // Set the extension
        $this->ext = mb_strtolower($ext);

        // Render the file
        $this->_render();

        // Download the file
        $this->_download($headers);
    }

    /**
     * Export and download the spreadsheet
     *
     * @param string $ext
     * @param array   $headers
     */
    public function download($ext = 'xlsx', array $headers = [])
    {
        $this->export($ext, $headers);
    }

    /**
     * Download a file
     *
     * @param  array $headers
     * @throws LaravelExcelException
     */
    protected function _download(array $headers = [])
    {
        // Set the headers
        $this->_setHeaders(
            $headers,
            [
                'Content-Type'        => $this->contentType,
                'Content-Disposition' => 'attachment; filename*=UTF-8\'\'' . rawurlencode($this->filename.'.'.$this->ext),
                'Expires'             => 'Mon, 26 Jul 1997 05:00:00 GMT', // Date in the past
                'Last-Modified'       => Chronos::now()->format('D, d M Y H:i:s'),
                'Cache-Control'       => 'cache, must-revalidate',
                'Pragma'              => 'public'
            ]
        );

        // Check if writer isset
        if (!$this->writer) {
            throw new LaravelExcelException('[ERROR] No writer was set.');
        }

        // Download
        $this->writer->save('php://output');

        // End the script to prevent corrupted xlsx files
        exit;
    }
}
