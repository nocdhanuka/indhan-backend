package com.dhanuka.indhan.Activity.HomeMVP

import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean

/**
 * Created by Shivam on 2/27/2018.
 */

interface HomeContracts {

    interface IHomeView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: HomeBean)
    }

    interface HomePresenter {

        fun onSubmitClick(mHomeRequest: String)
    }

    interface HomeInteractor {

        fun userHome(listener: HomeContracts.HomeInteractor.OnFinishedListener, model: String)

        interface OnFinishedListener {
            fun onHomeSucess(items: HomeBean)

            fun onHomeFailed(errorMessage: String)
        }
    }
}
