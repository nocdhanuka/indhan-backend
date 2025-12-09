package com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP

import com.dhanuka.indhan.Model.CommonModel
import com.dhanuka.indhan.Model.RegisterFarmerContactModel
import java.util.*

/**
 * Created by Shivam on 3/9/2018.
 */

interface SubmitFarmersContracts {

    interface ISubmitFarmersView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: CommonModel)
        fun onFailureFarmers(message: String)
    }

    interface SubmitFarmersPresenter {
        fun onSubmitClick(mSubmitFarmersRequest: HashMap<String, Any>)
        fun onSubmitClickOptimized(
            mSubmitFarmersRequest: ArrayList<RegisterFarmerContactModel>,
            token: String
        )
    }

    interface SubmitFarmersInteractor {

        fun userSubmitFarmers(
            listener: SubmitFarmersContracts.SubmitFarmersInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        fun userSubmitFarmersOptimized(
            listener: SubmitFarmersContracts.SubmitFarmersInteractor.OnFinishedListener,
            list: ArrayList<RegisterFarmerContactModel>,
            token: String
        )

        interface OnFinishedListener {
            fun onSubmitFarmersSucess(items: CommonModel)
            fun onSubmitFarmersFailed(errorMessage: String)
        }
    }
}
