package com.dhanuka.indhan.Activity.TicketDetailMVP

import com.dhanuka.indhan.Activity.TicketDetailMVP.Model.TicketDetailBean
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketDetailPresenterImpl(
    private val mITicketDetailView: TicketDetailContracts.ITicketDetailView?
) :
    TicketDetailContracts.TicketDetailPresenter,
    TicketDetailContracts.TicketDetailInteractor.OnFinishedListener {

    private val mTicketDetailInteractor: TicketDetailContracts.TicketDetailInteractor

    init {
        this.mTicketDetailInteractor = TicketDetailInteractorImpl()
    }


    override fun onSubmitClick(mTicketDetailRequest: HashMap<String, Any>) {
        mITicketDetailView?.showProgress()
        mTicketDetailInteractor.userTicketDetail(this, mTicketDetailRequest)
    }

    override fun onTicketDetailSucess(items: TicketDetailBean) {
        if (mITicketDetailView != null) {
            mITicketDetailView.setItems(items)
            mITicketDetailView.hideProgress()
        }
    }

    override fun onTicketDetailFailed(errorMessage: String) {}
}
