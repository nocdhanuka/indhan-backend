package com.dhanuka.indhan.Activity.ReportsMVP

import com.dhanuka.indhan.Activity.ReportsMVP.Model.ReportsBean

/**
 * Created by Shivam on 2/28/2018.
 */

interface ReportsContracts {

    interface IReportsView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: ReportsBean)
    }

    interface ReportsPresenter {

        fun onSubmitClick(mReportsRequest: String)
    }

    interface ReportsInteractor {

        fun userReports(listener: OnFinishedListener, model: String)

        interface OnFinishedListener {
            fun onReportsSucess(items: ReportsBean)

            fun onReportsFailed(errorMessage: String)
        }
    }
}
