package com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

interface DistrictContracts {

    interface IDistrictView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: DistrictModel)
    }

    interface DistrictPresenter {

        fun onSubmitClick(mDistrictRequest: HashMap<String, Any>)
    }

    interface DistrictInteractor {

        fun userDistrict(
            listener: DistrictContracts.DistrictInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        interface OnFinishedListener {
            fun onDistrictSucess(items: DistrictModel)

            fun onDistrictFailed(errorMessage: String)
        }
    }
}
