<?php
namespace Modules\Hotel\Models;

use App\BaseModel;
use Illuminate\Support\Facades\DB;

class HotelRoomTerm extends BaseModel
{
    protected $table = 'bravo_hotel_room_term';
    protected $fillable = [
        'term_id',
        'target_id'
    ];

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public static function getRoomTerms($roomId, $locale = 'en')
    {
        $room = HotelRoom::find($roomId);
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $attributes = DB::table('bravo_attrs')
            ->join('bravo_terms', 'bravo_terms.attr_id', '=', 'bravo_attrs.id')
            ->join('bravo_hotel_room_term', 'bravo_hotel_room_term.term_id', '=', 'bravo_terms.id')
            ->where('bravo_hotel_room_term.target_id', $roomId)
            ->select('bravo_attrs.id as attr_id', 'bravo_attrs.name as attr_name')
            ->distinct()
            ->get();

        $result = [];

        foreach ($attributes as $attribute) {
            $attributes = DB::table('bravo_attrs')
                ->join('bravo_terms', 'bravo_terms.attr_id', '=', 'bravo_attrs.id')
                ->join('bravo_hotel_room_term', 'bravo_hotel_room_term.term_id', '=', 'bravo_terms.id')
                ->where('bravo_hotel_room_term.target_id', $roomId)
                ->select('bravo_attrs.id as attr_id', 'bravo_attrs.name as attr_name')
                ->distinct()
                ->get();

            $result = [];

            foreach ($attributes as $attribute) {
                // 2. جلب أول 3 شروط لكل تصنيف بترتيبها في جدول bravo_terms
                $terms = DB::table('bravo_terms')
                    ->join('bravo_hotel_room_term', 'bravo_hotel_room_term.term_id', '=', 'bravo_terms.id')
                    ->leftJoin('bravo_terms_translations', function ($join) use ($locale) {
                        $join->on('bravo_terms.id', '=', 'bravo_terms_translations.origin_id')
                            ->where('bravo_terms_translations.locale', '=', $locale);
                    })
                    ->where('bravo_hotel_room_term.target_id', $roomId)
                    ->where('bravo_terms.attr_id', $attribute->attr_id)
                    ->select(
                        'bravo_terms.id as term_id',
                        DB::raw('COALESCE(bravo_terms_translations.name, bravo_terms.name) as term_name')
                    )
                    ->orderBy('bravo_terms.id') // ترتيب الشروط بناءً على ترتيبها في جدول bravo_terms
                    ->limit(3) // أخذ أول 3 شروط فقط
                    ->get();

                $result[] = [
                    'attribute_name' => $attribute->attr_name,
                    'terms' => $terms->map(function ($term) {
                        return [
                            'id' => $term->term_id,
                            'name' => $term->term_name,
                        ];
                    }),
                ];
            }

            return $result;
        }

        return $result;
    }
}
