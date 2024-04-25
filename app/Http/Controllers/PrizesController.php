<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\AwardedPrize;

use Illuminate\Http\Request;
use App\Http\Requests\PrizeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;



class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::with('awardedPrizes')->get();
             
        return view('prizes.index', ['prizes' => $prizes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $remaining_probability = 100 - Prize::sum('probability');
        $messages = [
            'probability.total_probability' => 'The total probability cannot exceed '.$remaining_probability.'%.',
        ];

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:prizes',
            'probability' => 'required|numeric|between:1,100|total_probability:' . Prize::sum('probability'),
        ], $messages);

        if ($validator->fails()) {
            return to_route('prizes.create')
                ->withErrors($validator)
                ->withInput();
        }

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index')->with('success', 'Prize created successfully!');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $prize = Prize::findOrFail($id);

        $remaining_probability = 100 - (Prize::sum('probability') - $prize->probability);

        $messages = [
            'probability.total_probability' => 'The total probability cannot exceed '.$remaining_probability.'%.',
            'probability.between' => 'The probability must be less than or equal '.$remaining_probability.'.'
        ];
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:prizes,title,'.$id,
            'probability' => 'required|numeric|between:0,'.$remaining_probability.'|total_probability:' . (Prize::sum('probability') - $prize->probability),
        ], $messages);

    
        if ($validator->fails()) {
            return redirect()->route('prizes.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        
        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index')->with('success', 'Prize updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


    public function simulate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number_of_prizes' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return to_route('prizes.index')
                ->withErrors($validator)
                ->withInput();
        }
 
        $number_of_prizes = $request->number_of_prizes;
        $awardedPrizes = session('awardedPrizes', []);
        $awardedPrizesPercent= session('awardedPrizesPercent', []);
       
        
        $number_of_total_prizes = (int)session('totalPrize')  + $number_of_prizes;

        session(['totalPrize' => $number_of_total_prizes]);

        $prizes = Prize::all();
        $totalProbability = $prizes->sum('probability');
        if ($totalProbability < 100) {
            return to_route('prizes.index')
                ->withErrors(['message' => 'Total probability of prizes must be 100%'])
                ->withInput();
        }
        
        foreach ($prizes as $prize) {
            Prize::nextPrize($prize, $number_of_total_prizes, $awardedPrizes,$awardedPrizesPercent);
        }

        session(['awardedPrizes' => $awardedPrizes]);
        session(['awardedPrizesPercent' => $awardedPrizesPercent]);
        
        return to_route('prizes.index');

    }



    public function reset()
    {
        session(['awardedPrizes' => []]);
        session(['awardedPrizesPercent' => []]);
        session(['totalPrize' => []]);

        // Reset database records
        AwardedPrize::truncate();
        return to_route('prizes.index');
    }
}