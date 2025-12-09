package com.dhanuka.indhan.PlanATP.View

import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel

/**
 * Created by John on 7/23/2018.
 */
interface CustomerCareFactory {
    interface CCHomeInterface {
        fun showProgress()
        fun hideProgress()
        fun OnSuccess(response: CCQueriesModel)
        fun onFailure(response: String)
    }

    interface CCSHomeInterface {
        fun showProgress()
        fun hideProgress()
        fun OnSuccess(response: CCQueriesForS1Model)
        fun onFailure(response: String)
    }

    interface CCQueryDetailHomeInterface {
        fun showProgress()
        fun hideProgress()
        fun OnSuccess(response: String)
        fun onFailure(response: String)
    }

    interface CCPresenter {
        fun onRequestQueries(token: String)
        fun onQueriesSuccess(response: CCQueriesModel)
        fun onFailure(response: String)
    }

    interface CCSPresenter {
        fun onRequestQueries(token: String)
        fun onQueriesSuccess(response: CCQueriesForS1Model)
        fun onFailure(response: String)
    }

    interface CCQueryDetailPresenter {
        fun OnUpdateRemark(token: HashMap<String, Any>)
        fun onUpdateRemarkSuccess(response: String)
        fun onFailure(response: String)
    }

    interface CCQueryDetailforS1Presenter {
        fun OnUpdateBDO(token: HashMap<String, Any>)
        fun onUpdateBdoSuccess(response: String)
        fun onFailure(response: String)
    }

}