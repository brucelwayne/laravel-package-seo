<?php

namespace Brucelwayne\SEO\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class FavoritePostUnCategorizedSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    private $posts;
    private $needs_image;

    public function __construct($posts, $needs_image)
    {
        $this->posts = $posts;
        $this->needs_image = $needs_image;
    }

    public function collection()
    {
        // Remove image URL from the data, only include Title and Price
        return $this->posts->map(function ($post) {
            return [
                'Image' => '',
                'Title' => $post->title ?? '',
                'Cost' => 0,
                'Price' => 0,
                'Shipping' => 0,
            ];
        });
    }

    public function headings(): array
    {
        return [
            '图片',
            '产品',
            '采购价',
            '售价',
            '快递费',
        ];
    }

    public function title(): string
    {
        return 'Uncategorized';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 设置列宽度，可以根据需要调整
                $sheet->getColumnDimension('A')->setWidth(20);  // 调整图片列宽度
                $sheet->getColumnDimension('B')->setWidth(40);  // 调整标题列宽度
                $sheet->getColumnDimension('C')->setWidth(15);  // 调整价格列宽度

                // 设置标题行的换行
                $sheet->getStyle('A1:C1')->getAlignment()->setWrapText(true);

                if ($this->needs_image) {
                    $rowIndex = 2; // Start inserting images from the second row
                    foreach ($this->posts as $post) {
                        $imageUrl = $post->payload['imgs'][0] ?? null;
                        if ($imageUrl) {
                            $this->insertImage($sheet, $imageUrl, 'A' . $rowIndex); // Draw image in column A
                        }
                        $rowIndex++;
                    }
                }
            },
        ];
    }

    private function insertImage($sheet, $url, $cellCoordinates)
    {
        try {
            // Fetch image data with timeout
            $context = stream_context_create(['http' => ['timeout' => 15]]);
            $imageData = @file_get_contents($url, false, $context);

            if (!$imageData) {
                return; // Could log this failure or proceed to next image
            }

            $imageResource = @imagecreatefromstring($imageData);
            if (!$imageResource) {
                return; // Could log this failure or proceed to next image
            }

            $drawing = new MemoryDrawing();
            $drawing->setName('Image');
            $drawing->setDescription('Post Image');
            $drawing->setImageResource($imageResource);
            $drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG); // JPEG might be lighter for memory than PNG
            $drawing->setMimeType(MemoryDrawing::MIMETYPE_JPEG);
            $drawing->setHeight(50); // Keep height reasonable
            $drawing->setCoordinates($cellCoordinates);
            $drawing->setWorksheet($sheet);

            // Free up memory by destroying the image resource after setting it to the worksheet
            imagedestroy($imageResource);
        } catch (\Exception $e) {
            // Log the error or handle it gracefully, e.g., with error logging
            error_log("Failed to insert image at {$cellCoordinates}: " . $e->getMessage());
        }
    }
}
