package com.dhanuka.indhan.Activity.NewTicketMVP.Model

import androidx.room.Entity
import androidx.room.PrimaryKey
import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/28/2018.
 */

@Entity
class DataSourceBean {
    @PrimaryKey(autoGenerate = true)
    var _id: Int? = null

    @SerializedName("dsID")
    @Expose
    var dsID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null
}
