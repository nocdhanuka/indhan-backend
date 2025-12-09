package com.dhanuka.indhan.Activity.ReportsMVP

import com.dhanuka.indhan.Activity.ReportsMVP.Model.ReportsBean

/**
 * Created by Shivam on 2/28/2018.
 */

class ReportsPresenterImpl(private val mIReportsView: ReportsContracts.IReportsView?) :
    ReportsContracts.ReportsPresenter, ReportsContracts.ReportsInteractor.OnFinishedListener {
    private val mReportsInteractor: ReportsContracts.ReportsInteractor

    init {
        this.mReportsInteractor = ReportsInteractorImpl()
    }


    override fun onSubmitClick(mReportsRequest: String) {
        mIReportsView?.showProgress()
        mReportsInteractor.userReports(this, mReportsRequest)
    }

    override fun onReportsSucess(items: ReportsBean) {
        if (mIReportsView != null) {
            mIReportsView.setItems(items)
            mIReportsView.hideProgress()
        }
    }

    override fun onReportsFailed(errorMessage: String) {}
}
