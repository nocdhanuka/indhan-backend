package com.dhanuka.indhan.EarningFilter

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemChipsBinding


class SelctedVillagesAdapter(var context: Context, var list: MutableList<MyVillageBean.MyVillageData>) :
    RecyclerView.Adapter<SelctedVillagesAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemChipsBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_chips, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size

    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemChipsBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtVillageName.text = list[position].villageName


        }


    }
}