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

    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    public function collection()
    {
        // Remove image URL from the data, only include Title and Price
        return $this->posts->map(function ($post) {
            return [
                'Image' => '',
                'Title' => $post->seoPost->title ?? '',
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

                $rowIndex = 2; // Start inserting images from the second row
                foreach ($this->posts as $post) {
                    $imageUrl = $post->seoPost->payload['imgs'][0] ?? null;
                    if ($imageUrl) {
                        $this->insertImage($sheet, $imageUrl, 'A' . $rowIndex); // Draw image in column A
                    }
                    $rowIndex++;
                }
            },
        ];
    }

    private function insertImage($sheet, $url, $cellCoordinates)
    {
        try {
            $imageData = file_get_contents($url);
            if (!$imageData) {
                throw new \Exception("Unable to fetch image from URL: $url");
            }

            $imageResource = @imagecreatefromstring($imageData);
            if (!$imageResource) {
                throw new \Exception("Invalid image resource from URL: $url");
            }

            $drawing = new MemoryDrawing();
            $drawing->setName('Image');
            $drawing->setDescription('Post Image');
            $drawing->setImageResource($imageResource);
            $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
            $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
            $drawing->setHeight(50); // Adjust height as needed
            $drawing->setCoordinates($cellCoordinates);
            $drawing->setWorksheet($sheet);
        } catch (\Exception $e) {
            // Log error or handle gracefully
        }
    }
}
