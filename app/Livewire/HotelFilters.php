<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;

class HotelFilters extends Component
{
    public Collection $hotelStarCounts;
    public Collection $reviewScoreCounts;
    public array $selectedStars = [];
    public array $selectedReviews = [];

    // 🔹 تأكد من أن Livewire يربط الفلاتر مع الـ URL
    protected $queryString = [
        'selectedStars' => ['as' => 'star_rate', 'except' => []],
        'selectedReviews' => ['as' => 'review_score', 'except' => []],
    ];

    protected $listeners = ['updateCounts' => 'updateCounts'];

    public function mount(Collection $hotelStarCounts, Collection $reviewScoreCounts)
    {
        $this->hotelStarCounts = $hotelStarCounts;
        $this->reviewScoreCounts = $reviewScoreCounts;

        // 🔹 تحميل القيم من الـ URL عند تحميل الصفحة
        $this->selectedStars = request()->query('star_rate', []);
        $this->selectedReviews = request()->query('review_score', []);
    }

    public function updateCounts($hotelStarCounts, $reviewScoreCounts)
    {
        $this->hotelStarCounts = collect($hotelStarCounts);
        $this->reviewScoreCounts = collect($reviewScoreCounts);
    }

    public function updatedSelectedStars()
    {
        // 🔹 تحديث القيم في الـ URL عند تغيير الفلاتر
        $this->dispatchBrowserEvent('update-url');
    }

    public function updatedSelectedReviews()
    {
        // 🔹 تحديث القيم في الـ URL عند تغيير الفلاتر
        $this->dispatchBrowserEvent('update-url');
    }

    public function render()
    {
        return view('livewire.hotel-filters');
    }
}
