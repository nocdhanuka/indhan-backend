package com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP

import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model.EndTicketBean
import java.util.*

/**
 * Created by Shivam on 3/7/2018.
 */

class EndTicketPresenterImpl(private val mIEndTicketView: EndTicketContracts.IEndTicketView?) :
    EndTicketContracts.EndTicketPresenter,
    EndTicketContracts.EndTicketInteractor.OnFinishedListener {
    private val mEndTicketInteractor: EndTicketContracts.EndTicketInteractor

    init {
        this.mEndTicketInteractor = EndTicketInteractorImpl()
    }


    override fun onSubmitClick(mEndTicketRequest: HashMap<String, Any>) {
        mIEndTicketView?.showProgress()
        mEndTicketInteractor.userEndTicket(this, mEndTicketRequest)
    }

    override fun onEndTicketSucess(items: EndTicketBean) {
        if (mIEndTicketView != null) {
            mIEndTicketView.setItems(items)
            mIEndTicketView.hideProgress()
        }
    }

    override fun onEndTicketFailed(errorMessage: String) {}
}
