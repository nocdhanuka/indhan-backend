package com.dhanuka.indhan.Profile

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemProfileBinding


class ProfileVillageAdapter(
    var context: Context,
    var list: MutableList<DDProfileBean.Data.MyVillage>
) : RecyclerView.Adapter<ProfileVillageAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemProfileBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_profile_, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return if (list.size >= 4) {
            4
        } else {
            list.size
        }
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemProfileBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtCrop.text = list[position].village
            view.txtFarmers.text = list[position].totalfarmer


        }


    }
}