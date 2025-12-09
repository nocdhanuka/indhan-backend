package com.dhanuka.indhan.Activity.RegisterFarmersMVP

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.RegisterFarmersBean

/**
 * Created by Shivam on 3/8/2018.
 */

interface RegisterFarmersContracts {

    interface IRegisterFarmersView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: RegisterFarmersBean)
    }

    interface RegisterFarmersPresenter {

        fun onSubmitClick(mRegisterFarmersRequest: String)
    }

    interface RegisterFarmersInteractor {

        fun userRegisterFarmers(listener: OnFinishedListener, model: String)

        interface OnFinishedListener {
            fun onRegisterFarmersSucess(items: RegisterFarmersBean)
            fun onRegisterFarmersFailed(errorMessage: String)
        }
    }
}
