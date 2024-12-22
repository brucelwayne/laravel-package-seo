<?php

namespace Brucelwayne\SEO\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Mallria\App\Facades\AppFacade;
use Mallria\Core\Facades\TenantFacade;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        return $this->posts->map(function ($post) {

            $link = route('business.admin.app.workspace.external.post.single', [
                'post' => $post->hash,
                'tenant' => TenantFacade::get()->hash,
                'app' => AppFacade::get()->hash,
            ]);

            return [
                'Image' => '',
                'Link' => "=HYPERLINK(\"$link\", \"" . '链接' . "\")",
                'Title' => $post->title ?? '',
                'Cost' => 0,
                'Price' => 0,
                'Shipping' => 0,
                'Profit' => '', // 毛利
                'ProfitMargin' => '', // 毛利率
                'BreakEvenQuantity' => '', // 平衡数量
                'CPC' => '', // CPC成本
                'CPA' => '', // CPA成本
                'Revenue' => '', // 收入
                'TotalCost' => '', // 总成本
            ];
        });
    }

    public function headings(): array
    {
        return [
            '图片',
            '产品链接',
            '产品名称',
            '采购价',
            '售价',
            '快递费',
            '毛利',
            '毛利率',
            '平衡数量',
            'CPC',
            'CPA',
            '收入',
            '总成本',
        ];
    }

    public function title(): string
    {
        if (empty($this->category)) {
            return 'Uncategorized';
        }
        return $this->category->name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 为 C 列（产品名称）启用自动换行
                $sheet->getStyle('C')->getAlignment()->setWrapText(true);

                // 设置 C 列宽度（可选）
                $sheet->getColumnDimension('C')->setWidth(30);

                // 设置标题行样式（可选）
                $sheet->getStyle('A1:M1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}



