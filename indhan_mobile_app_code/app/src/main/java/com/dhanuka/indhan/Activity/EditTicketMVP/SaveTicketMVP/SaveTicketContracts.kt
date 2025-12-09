package com.dhanuka.indhan.Activity.EditTicketMVP.SaveTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

interface SaveTicketContracts {

    interface ISaveTicketView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: SubmitTicketBean)
    }

    interface SaveTicketPresenter {

        fun onSubmitClick(mSaveTicketRequest: HashMap<String, Any>)
    }

    interface SaveTicketInteractor {

        fun userSaveTicket(
            listener: SaveTicketContracts.SaveTicketInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        interface OnFinishedListener {
            fun onSaveTicketSucess(items: SubmitTicketBean)

            fun onSaveTicketFailed(errorMessage: String)
        }
    }
}
