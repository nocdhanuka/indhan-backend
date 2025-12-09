package com.dhanuka.indhan.Reports.Reports_MVP

import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.dhanuka.indhan.Reports.Model.ProductListResponse
import com.dhanuka.indhan.Reports.Model.ReportsResponse

interface ReportsFactory {
    interface ReportsHomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: ReportsResponse)
        fun onEfficiencySuccess(model: EfficiencyResponse)
        fun onEfficiencyFailure(model: String)
        fun onFailure(mes: String)
    }

    interface ProductHomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: ProductListResponse)
        fun onFailure(mes: String)
    }

    interface ReportsPresenter {
        fun onFailure(token: String)
        fun onRequestReportsData(model: HashMap<String, Any>)
        fun onRequestEfficiency(token: String, date: String)
        fun onSuccessUpload(response: ReportsResponse)
        fun onEfficiencySuccess(response: EfficiencyResponse)
        fun onEfficiencyFailure(model: String)
    }

    interface ProductListPresenter {
        fun onFailure(token: String)
        fun onRequestProductList(model: String)
        fun onSuccess(response: ProductListResponse)
    }


}