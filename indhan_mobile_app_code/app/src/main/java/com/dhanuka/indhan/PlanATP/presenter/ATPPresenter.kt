package com.dhanuka.indhan.PlanATP.presenter

import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanAtpSubmitResponseModel

/**
 * Created by John on 5/30/2018.
 */
interface ATPPresenter {
    fun onRequest(token: String, model: ArrayList<PlanAtpDBModel>)
    fun onSuccess(token: PlanAtpSubmitResponseModel)
    fun onRequestDataResultFailure(msg: String)

    interface AtpUI {
        fun onSuccess()
        fun showProgress()
        fun hideProgress()
        fun onFailureATPshare(msg: String?)
    }

}
