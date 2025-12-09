package com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

interface TehsilContracts {

    interface ITehsilView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: TehsilModel)
    }

    interface TehsilPresenter {

        fun onSubmitClick(mTehsilRequest: HashMap<String, Any>)
    }

    interface TehsilInteractor {

        fun userTehsil(
            listener: TehsilContracts.TehsilInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        interface OnFinishedListener {
            fun onTehsilSucess(items: TehsilModel)

            fun onTehsilFailed(errorMessage: String)
        }
    }
}
