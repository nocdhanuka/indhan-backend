package com.dhanuka.indhan.farmerlist

import java.util.*

interface FarmerListConractor {

    interface MyFarmerListView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: FarmerBean)

        fun failure(items: String)
    }

    interface FarmerListPresenter {

        fun onGetFarmerList(param: HashMap<String, Any>)
    }

    interface FarmerListInteractor {

        fun onFarmerList(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onFarmerListSuccess(items: FarmerBean)

            fun onFarmerListFailed(errorMessage: String)
        }
    }
}