package com.dhanuka.indhan.EarningFilter

import com.dhanuka.indhan.MyVillage.MyVillageBean
import java.util.*

interface EarningFilterContractor {
    interface EarningFilterView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: MyVillageBean)
        fun failure(items: String)
    }

    interface EarningFilterPresenter {

        fun onVillageList(mParam: HashMap<String, Any>)

    }

    interface EarningFilterInteractor {
        fun onVillageList(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onVillageSuccess(items: MyVillageBean)
            fun onVillageFailed(errorMessage: String)
        }
    }
}