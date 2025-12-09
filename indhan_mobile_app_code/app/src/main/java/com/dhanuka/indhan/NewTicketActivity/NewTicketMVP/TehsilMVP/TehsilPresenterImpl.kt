package com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class TehsilPresenterImpl(private val mITehsilView: TehsilContracts.ITehsilView?) :
    TehsilContracts.TehsilPresenter, TehsilContracts.TehsilInteractor.OnFinishedListener {
    private val mTehsilInteractor: TehsilContracts.TehsilInteractor

    init {
        this.mTehsilInteractor = TehsilInteractorImpl()
    }


    override fun onSubmitClick(mTehsilRequest: HashMap<String, Any>) {
        mITehsilView!!.showProgress()
        mTehsilInteractor.userTehsil(this, mTehsilRequest)
    }

    override fun onTehsilSucess(items: TehsilModel) {
        if (mITehsilView != null) {
            mITehsilView.setItems(items)
            // mITehsilView.hideProgress()
        }
    }

    override fun onTehsilFailed(errorMessage: String) {
        mITehsilView!!.hideProgress()
    }
}
