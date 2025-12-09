package com.dhanuka.indhan.BDOPlanFilterActivity.MVP

import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse

/**
 * Created by John on 6/8/2018.
 */
interface BDOFilterFactory {
    interface BDOFilterView {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: BDOFilterResponse)
        fun onFailure(msg: String)
    }

    interface BDOFilterPresenter {
        fun onRequest(token: String)
        fun onSuccess(token: BDOFilterResponse)
        fun onFailure(msg: String)
    }
}