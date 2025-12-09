package com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/5/2018.
 */

class SubmitTicketBean {

    @SerializedName("status_code")
    @Expose
    var status_code: Int? = null

    @SerializedName("message")
    @Expose
    var message: String? = null

    @SerializedName("status")
    @Expose
    var status: String? = null

    @SerializedName("data")
    @Expose
    var data: String? = null

    var ticket_id: String? = null
}
