package com.dhanuka.indhan.myteam

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemDdHistoryBinding


class DDHistoryAdapter(
    var context: Context,
    var list: MutableList<DDProfileDetails.Data.DdHistory>
) : RecyclerView.Adapter<DDHistoryAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemDdHistoryBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_dd_history, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemDdHistoryBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtCrop.text = list[position].name
            view.txtTicketId.text = list[position].from
            view.txtFarmers.text = list[position].end


        }


    }
}