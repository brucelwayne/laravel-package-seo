<?php

namespace Brucelwayne\SEO\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class FavoritePostCategorizedSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    private $category;
    private $posts;

    public function __construct($category, $posts)
    {
        $this->category = $category;
        $this->posts = $posts;
    }

    public function collection()
    {
        // Map the posts to the required format (exclude image URLs here)
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
        return $this->category->name; // Category name as sheet title
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 设置列宽度，可以根据需要调整
                $sheet->getColumnDimension('A')->setWidth(20);  // 设置图片列宽度
                $sheet->getColumnDimension('B')->setWidth(40);  // 设置标题列宽度
                $sheet->getColumnDimension('C')->setWidth(15);  // 设置价格列宽度

                // 设置标题行的换行
                $sheet->getStyle('A1:C1')->getAlignment()->setWrapText(true);

                // 可选: 设置标题行的文字居中
                $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1:C1')->getAlignment()->setVertical('center');

                // 在第二行开始插入图片
                $rowIndex = 2;
                foreach ($this->posts as $post) {
                    $imageUrl = $post->seoPost->payload['imgs'][0] ?? null;
                    if ($imageUrl) {
                        $this->insertImage($sheet, $imageUrl, 'A' . $rowIndex); // 插入图片到A列
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
                return;
            }

            $imageResource = @imagecreatefromstring($imageData);
            if (!$imageResource) {
                return;
            }

            $drawing = new MemoryDrawing();
            $drawing->setName('Image');
            $drawing->setImageResource($imageResource);
            $drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
            $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setCoordinates($cellCoordinates); // Image inserted at specified cell
            $drawing->setHeight(50); // Adjust image height
            $drawing->setWorksheet($sheet);
        } catch (\Exception $e) {
            // Log error or handle gracefully
        }
    }
}

