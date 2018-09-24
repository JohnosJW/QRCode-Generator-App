<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQrcodeRequest;
use App\Http\Requests\UpdateQrcodeRequest;
use App\Models\Qrcode;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\QrcodeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Criteria\RequestCriteria;
use QR_Code\QR_Code;
use Response;
use QRCode as QRCodeGenerator;
use Auth;
use App\Http\Resources\Qrcode as QrcodeResource;
use App\Http\Resources\QrcodeCollection as QrcodeResourceCollection;

class QrcodeController extends AppBaseController
{
    /** @var  QrcodeRepository */
    private $qrcodeRepository;

    public function __construct(QrcodeRepository $qrcodeRepo)
    {
        $this->qrcodeRepository = $qrcodeRepo;
    }

    /**
     * Display a listing of the Qrcode.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->role_id < 3) {
            $this->qrcodeRepository->pushCriteria(new RequestCriteria($request));
            $qrcodes = $this->qrcodeRepository->all();
        } else {
            $qrcodes = Qrcode::where('user_id', Auth::user()->id)->get();
        }

//        return new QrcodeResourceCollection($qrcodes);

        return view('qrcodes.index')
            ->with('qrcodes', $qrcodes);
    }

    /**
     * @param Request $request
     */
    public function showPaymentPage(Request $request)
    {
       $inputs = $request->all();

       $user = User::where('email', $inputs['email'])->first();

       if (empty($user)) {
           $user = User::create([
               'name' => $inputs['email'],
               'email' => $inputs['email'],
               'password' => Hash::make($inputs['email'])
           ]);
       }

       $qrcode = Qrcode::where('id', $inputs['qrcode_id'])->first();

       $transaction = Transaction::create([
           'user_id' => $user->id,
           'qrcode_id' => $qrcode->id,
           'status' => 'initiated',
           'qrcode_owner_id' => $qrcode->user_id,
           'payment_method' => 'paystack/card',
           'amount' => $qrcode->amount
       ]);

       return view('qrcodes.paystack-form', [
           'qrcode' => $qrcode,
           'transaction' => $transaction,
           'user' => $user
       ]);
    }

    /**
     * Show the form for creating a new Qrcode.
     *
     * @return Response
     */
    public function create()
    {
        return view('qrcodes.create');
    }

    /**
     * Store a newly created Qrcode in storage.
     *
     * @param CreateQrcodeRequest $request
     *
     * @return Response
     */
    public function store(CreateQrcodeRequest $request)
    {
        $input = $request->all();

        $qrcode = $this->qrcodeRepository->create($input);

        $file = 'generated_qrcodes/' . $qrcode->id . '.png';

        $newQrcode = QRCodeGenerator::text("message")
            ->setSize(8)
            ->setMargin(2)
            ->setOutfile($file)
            ->png();

        $input['qrcode_path'] = $file;

        $newQrcode = Qrcode::where('id', $qrcode->id)
            ->update([
                'qrcode_path' => $input['qrcode_path']
            ]);

        if ($newQrcode) {
            Flash::success('Qrcode saved successfully.');
        } else {
            Flash::error('Qrcode failed to save successfully.');
        }

        return redirect(route('qrcodes.show', ['qrcode' => $qrcode]));
    }

    /**
     * Display the specified Qrcode.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id = null)
    {
        $qrcode = $this->qrcodeRepository->findWithoutFail($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        $transactions = $qrcode->transactions;

        return view('qrcodes.show')
            ->with('qrcode', $qrcode)
            ->with('transactions', $transactions);
    }

    /**
     * Show the form for editing the specified Qrcode.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $qrcode = $this->qrcodeRepository->findWithoutFail($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        return view('qrcodes.edit')->with('qrcode', $qrcode);
    }

    /**
     * Update the specified Qrcode in storage.
     *
     * @param  int              $id
     * @param UpdateQrcodeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQrcodeRequest $request)
    {
        $qrcode = $this->qrcodeRepository->findWithoutFail($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        $qrcode = $this->qrcodeRepository->update($request->all(), $id);

        Flash::success('Qrcode updated successfully.');

        return redirect(route('qrcodes.show', ['qrcode' => $qrcode]));
    }

    /**
     * Remove the specified Qrcode from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $qrcode = $this->qrcodeRepository->findWithoutFail($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        $this->qrcodeRepository->delete($id);

        Flash::success('Qrcode deleted successfully.');

        return redirect(route('qrcodes.index'));
    }
}
