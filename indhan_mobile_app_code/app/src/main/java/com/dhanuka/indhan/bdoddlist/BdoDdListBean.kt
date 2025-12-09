package com.dhanuka.indhan.bdoddlist

import androidx.room.Entity
import androidx.room.PrimaryKey

data class BdoDdListBean(
    val `data`: List<Data>,
    val message: String,
    val status: String,
    val status_code: Int
) {
    @Entity(tableName = "dd_list")
    data class Data(
        val ECN: String,
        val dd_id: String,
        val name: String,
        var isChecked: Boolean
    ) {

        @PrimaryKey(autoGenerate = true)
        var _id: Int? = null
    }
}