package com.dhanuka.indhan.Activity.NewTicketMVP

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_data_source_spinner.view.*

/**
 * Created by Shivam on 2/28/2018.
 */

class DataSourceSpinnerAdapter(
    @get:JvmName("getContext_") private val context: Context,
    private val resource: Int
) : ArrayAdapter<Context>(context, resource) {
    private var dataList: List<DataSourceBean>? = null

    fun setData(dataList: List<DataSourceBean>) {
        this.dataList = dataList
        notifyDataSetChanged()
    }

    override fun getCount(): Int {
        return if (dataList == null) 0 else dataList!!.size
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup): View {
        val itemView =
            LayoutInflater.from(context).inflate(R.layout.item_data_source_spinner, parent, false)
        itemView.dataSourceName.text = dataList?.get(position)?.item
        return itemView
    }

    override fun getDropDownView(position: Int, convertView: View?, parent: ViewGroup): View {
        val itemView =
            LayoutInflater.from(context).inflate(R.layout.item_data_source_spinner, parent, false)
        itemView.dataSourceName.text = dataList?.get(position)?.item
        return itemView
    }
}
