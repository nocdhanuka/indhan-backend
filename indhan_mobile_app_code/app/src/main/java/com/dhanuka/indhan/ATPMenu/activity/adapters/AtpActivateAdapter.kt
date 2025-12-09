package com.dhanuka.indhan.ATPMenu.activity.adapters

import android.content.Context

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.ATPBean

import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_atp_product_name.view.*
import java.util.*

class AtpActivateAdapter(context: Context, atpBean: ArrayList<ATPBean.Data.Product>) :
    RecyclerView.Adapter<AtpActivateAdapter.ViewHolder>() {
    var context: Context
    var atpBean: ArrayList<ATPBean.Data.Product>

    init {
        this.context = context
        this.atpBean = atpBean
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_atp_product_name, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return atpBean.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtProductNameAtp.text = atpBean.get(position).product


    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }


}