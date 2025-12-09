package com.dhanuka.indhan.MyVillage

import java.util.*

interface MyVillageContractor {

    interface MyVillageView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: MyVillageBean)
        fun failure(items: String)
    }

    interface MyVillagePresenter {

        fun onGetMyVillage(mMyVillage: HashMap<String, Any>,isProgress:Boolean)
    }

    interface MyVillageInteractor {

        fun ddMyVillage(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onVillageSuccess(items: MyVillageBean)

            fun onVillageFailed(errorMessage: String)
        }
    }
}