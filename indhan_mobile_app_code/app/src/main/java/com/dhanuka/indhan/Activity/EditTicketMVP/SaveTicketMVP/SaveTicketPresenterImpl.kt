package com.dhanuka.indhan.Activity.EditTicketMVP.SaveTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class SaveTicketPresenterImpl(private val mISaveTicketView: SaveTicketContracts.ISaveTicketView?) :
    SaveTicketContracts.SaveTicketPresenter,
    SaveTicketContracts.SaveTicketInteractor.OnFinishedListener {
    private val mSaveTicketInteractor: SaveTicketContracts.SaveTicketInteractor

    init {
        this.mSaveTicketInteractor = SaveTicketInteractorImpl()
    }


    override fun onSubmitClick(mSaveTicketRequest: HashMap<String, Any>) {
        mISaveTicketView?.showProgress()
        mSaveTicketInteractor.userSaveTicket(this, mSaveTicketRequest)
    }

    override fun onSaveTicketSucess(items: SubmitTicketBean) {
        if (mISaveTicketView != null) {
            mISaveTicketView.setItems(items)
            mISaveTicketView.hideProgress()
        }
    }

    override fun onSaveTicketFailed(errorMessage: String) {}
}
