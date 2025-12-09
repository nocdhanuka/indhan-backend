package com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP


import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.MyVillage.MyVillageBean
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

interface SubmitTicketContracts {

    interface ISubmitTicketView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: SubmitTicketBean)

        fun setVillageList(items: MyVillageBean)

    }

    interface SubmitTicketPresenter {

        fun onSubmitClick(mSubmitTicketRequest: HashMap<String, Any>)

        fun onVillageList(visit: HashMap<String, Any>)

    }

    interface SubmitTicketInteractor {

        fun userSubmitTicket(
            listener: SubmitTicketContracts.SubmitTicketInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        fun villageList(
            listener: SubmitTicketContracts.SubmitTicketInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )


        interface OnFinishedListener {
            fun onSubmitTicketSucess(items: SubmitTicketBean)

            fun onVillageList(items: MyVillageBean)


            fun onSubmitTicketFailed(errorMessage: String)
        }
    }
}
