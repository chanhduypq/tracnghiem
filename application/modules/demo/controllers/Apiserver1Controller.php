<?php

class Demo_Apiserver1Controller extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction() {
        $data = $this->_getAllParams();
        if ($data['format'] == 'xml') {
            if ($data['method'] == 'getListings') {
                $response = '<GetListingsResponse>
                            <Ack>Success</Ack>
                            <Version>140</Version>
                            <Timestamp>2015-08-05T17:22:38.00Z</Timestamp>
                            <TotalListingCount>35</TotalListingCount>
                            <Listings>
                            <Listing>
                            <ProductId>13658337</ProductId>
                            <Title>Paty Ceiling Light</Title>
                            <Description>Designed by Enzo Ciampalini, made in Italy.</Description>
                            <SKU>h-1234</SKU>
                            <CategoryId>20002</CategoryId>
                            <SKU>h-1234</SKU>
                            <CategoryId>20002</CategoryId>
                            <Price>123.99</Price>
                            <Currency>USD</Currency>
                            <Manufacturer>Lamp International</Manufacturer>
                            <MPN>h-1234</MPN>
                            <Style>Modern</Style>
                            <MSRP>199.99</MSRP>
                            <Quantity>15</Quantity>
                            <Status>Active</Status>
                            <RelationshipType>None</RelationshipType>
                            <ProductSpec>
                            <Width>3</Width>
                            <Height>5</Height>
                            <Depth>4</Depth>
                            <DimensionsUnit>IN</DimensionsUnit>
                            <Weight>160</Weight>
                            <WeightUnit>OZ</WeightUnit>
                            </ProductSpec>
                            <ShippingDetails>
                            <LeadTimeMin>1</LeadTimeMin>
                            <LeadTimeMax>2</LeadTimeMax>
                            </ShippingDetails>
                            <ReturnPolicyDetails>
                            <ReturnPolicy>Can be returned in 30 days.</ReturnPolicy>
                            <DamagePolicy>If damaged, will exchange for new
                            item.</DamagePolicy>
                            <Warranty>2-year warranty.</Warranty>
                            </ReturnPolicyDetails>
                            </Listing>
                            </Listings>
                        </GetListingsResponse>';
            } else if ($data['method'] == 'getListing') {
                $response = '<GetListingResponse>
                                <Ack>Success</Ack>
                                <Version>140</Version>
                                <Timestamp>2015-08-05T17:22:38.00Z</Timestamp>
                                <Listing>
                                <ProductId>13658337</ProductId>
                                <Title>Paty Ceiling Light</Title>
                                <Description>Designed by Enzo Ciampalini, made in Italy.</Description>
                                <SKU>h-1234</SKU>
                                <CategoryId>20002</CategoryId>
                                <Price>123.99</Price>
                                <Currency>USD</Currency>
                                <Manufacturer>Lamp International</Manufacturer>
                                <MPN>h-1234</MPN>
                                <Style>Modern</Style>
                                <MSRP>199.99</MSRP>
                                <Quantity>15</Quantity>
                                <Status>Active</Status>
                                <RelationshipType>None</RelationshipType>
                                <ProductSpec>
                                <Width>3</Width>
                                <Height>5</Height>
                                <Depth>4</Depth>
                                <DimensionsUnit>IN</DimensionsUnit>
                                <Weight>160</Weight>
                                <WeightUnit>OZ</WeightUnit>
                                </ProductSpec>
                                <ShippingDetails>
                                <LeadTimeMin>1</LeadTimeMin>
                                <LeadTimeMax>2</LeadTimeMax>
                                </ShippingDetails>
                                <ReturnPolicyDetails>
                                <ReturnPolicy>Can be returned in 30 days.</ReturnPolicy>
                                <DamagePolicy>If damaged, will exchange for new item.</DamagePolicy>
                                <Warranty>2-year warranty.</Warranty>
                                </ReturnPolicyDetails>
                                </Listing>
                            </GetListingResponse>
                            ';
            } else if ($data['method'] == 'getOrder') {
                $response = '<GetOrderResponse>
                                <Ack>Success</Ack>
                                <Version>149</Version>
                                <Timestamp>2016-02-11T19:17:28.00Z</Timestamp>
                                <Order>
                                <OrderId>1501-6406-7343-5848</OrderId>
                                <ShipmentId>1501-6406-7343-5848</ShipmentId>
                                <Created>2015-05-19 15:55:42</Created>
                                <Status>Shipped</Status>
                                <CustomerName>John Doe</CustomerName>
                                <Address>
                                <Address>310 University Avenue</Address>
                                <City>Palo Alto</City>
                                <State>CA</State>
                                <Country>US</Country>
                                <Zip>94301</Zip>
                                <Phone>6505555555</Phone>
                                </Address>
                                <ShippingMethod>Expedited</ShippingMethod>
                                <ShippingDescription>Expedited</ShippingDescription>
                                <FlatShipping>40.00</FlatShipping>
                                <OrderTotal>1499.00</OrderTotal>
                                <OrderItems>
                                <OrderItem>
                                <SKU>a-1234</SKU>
                                <ProductId>13661711</ProductId>
                                <Title>Red Leather Sofa Sectional</Title>
                                <Quantity>1</Quantity>
                                <Price>1459.00</Price>
                                <Tax>0.00</Tax>
                                <Shipping>18.73</Shipping>
                                <Type>Product</Type>
                                </OrderItem>
                                </OrderItems>
                                </Order>
                            </GetOrderResponse>';
            } else if ($data['method'] == 'getOrders') {
                $response = '<GetOrdersResponse>
                                <Ack>Success</Ack>
                                <Version>140</Version>
                                <Timestamp>2015-08-05T19:18:51.00Z</Timestamp>
                                <VisitorToken>1438802330_af704946-562e-40a4-9761-30e227d7b650_dc31dbc6a501c033cf0c2
                                c8383ff93b9</VisitorToken>
                                <Orders>
                                <Order>
                                <OrderId>1494-9440-5092-1367</OrderId>
                                <ShipmentId>1494-9440-5092-1367</ShipmentId>
                                <Created>2015-03-06 16:55:45</Created>
                                <Status>Shipped</Status>
                                <CustomerName>John Doe</CustomerName>
                                <Address>
                                <Address>310 University Avenue</Address>
                                <City>Palo Alto</City>
                                <State>CA</State>
                                <Zip>94301</Zip>
                                <Country>US</Country>
                                <Phone>6505555555</Phone>
                                </Address>
                                <ShippingMethod>Standard</ShippingMethod>
                                <FlatShipping>0.00</FlatShipping>
                                <OrderTotal>79.00</OrderTotal>
                                <OrderItems>
                                <OrderItem>
                                <SKU>33</SKU>
                                <ProductId>13661853</ProductId>
                                <Title>Track Lighting</Title>
                                <Quantity>1</Quantity>
                                <Price>79.00</Price>
                                <Tax>0.00</Tax>
                                <Shipping>0.00</Shipping>
                                <Type>Product</Type>
                                </OrderItem>
                                </OrderItems>
                                </Order>
                                </Orders>
                            </GetOrdersResponse>';
            } else if ($data['method'] == 'getPayments') {
                $response = '<GetPaymentsResponse>
                                <Ack>Success</Ack>
                                <Version>140</Version>
                                <Timestamp>2015-09-03T16:57:16.00Z</Timestamp>
                                <Payments>
                                <PaymentId>2</PaymentId>
                                </Payments>
                            </GetPaymentsResponse>';
            } else if ($data['method'] == 'getTransactions') {
                $response = '<GetTransactionsResponse>
                                <Ack>Success</Ack>
                                <Version>149</Version>
                                <Timestamp>2016-05-18T19:21:46.00Z</Timestamp>
                                <Payment>
                                <VendorType>mp</VendorType>
                                <FromDate>2014-12-01 00:00:00</FromDate>
                                <ToDate>2015-01-15 00:00:00</ToDate>
                                <Sales>139.00</Sales>
                                <Shipping>0.02</Shipping>
                                <Tax>0.00</Tax>
                                <Commission>-20.85</Commission>
                                <DepositAmount>118.17</DepositAmount>
                                <Transactions>
                                <Transaction>
                                <OrderId>1457-4207-8449-6627</OrderId>
                                <Subtotal>139.00</Subtotal>
                                <Shipping>0.02</Shipping>
                                <Tax>0.00</Tax>
                                <Commission>-20.85</Commission>
                                <Total>118.17</Total>
                                </Transaction>
                                </Transactions>
                                </Payment>
                            </GetTransactionsResponse>';
            }
        } else {
            $response = '';
        }
        $this->getResponse()->setBody($response);
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction() {
        $data = $this->_getAllParams();
        unset($data['module']);
        unset($data['controller']);
        unset($data['action']);
        $this->getResponse()->setBody(json_encode($data));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction() {
        $data = $this->_getAllParams();
        unset($data['module']);
        unset($data['controller']);
        unset($data['action']);
        $response = '';
        if ($data['method'] == 'addListing') {
            $response = '<AddListingResponse>
                                <Ack>Success</Ack>
                                <Version>140</Version>
                                <Timestamp>2015-08-05T19:02:11.00Z</Timestamp>
                                <SKU>Test</SKU>
                                <ProductId>13661668</ProductId>
                            </AddListingResponse>';
        } else if ($data['method'] == 'updateListing') {
            $response = '<UpdateListingResponse>
                                <Ack>Success</Ack>
                                <Version>140</Version>
                                <Timestamp>2015-08-05T19:04:14.00Z</Timestamp>
                                <VisitorToken>1438801454_1c0c3e66-3e0c-49fa-9ebe-a9bcb0350287_a3be6dd51afe87a421899
                                032a8d9ce69</VisitorToken>
                                <ProductId>4931085</ProductId>
                                <SKU>GYCode</SKU>
                            </UpdateListingResponse>';
        } else if ($data['method'] == 'updateInventory') {
            $response = '<UpdateInventoryResponse>
                            <Ack>Success</Ack>
                            <Version>140</Version>
                            <Timestamp>2015-08-05T17:56:24.00Z</Timestamp>
                            <VisitorToken>1438797383_53b01e47-74f2-4185-a833-f68808502bd5_60443e0847ee794bc1507
                            2d8aabb463b</VisitorToken>
                            <ProductId>13661871</ProductId>
                            <SKU>test</SKU>
                        </UpdateInventoryResponse>';
        } else if ($data['method'] == 'deleteListing') {
            $response = '<DeleteListingResponse>
                            <Ack>Success</Ack>
                            <Version>140</Version>
                            <Timestamp>2015-08-05T19:04:14.00Z</Timestamp>
                            <SKU>h-1234</SKU>
                        </DeleteListingResponse>';
        } else if ($data['method'] == 'updateOrder') {
            $response = '<UpdateOrderResponse>
                            <Ack>Success</Ack>
                            <Version>140</Version>
                            <Timestamp>2015-08-05T17:56:24.00Z</Timestamp>
                            <VisitorToken>1438797383_53b01e47-74f2-4185-a833-f68808502bd5_60443e0847ee794bc1507
                            2d8aabb463b</VisitorToken>
                            <OrderId>3451-1223-4987-0908</OrderId>
                        </UpdateOrderResponse>';
        }
        


        $this->getResponse()->setBody($response);
//        $this->getResponse()->setBody($this->getRequest()->getRawBody() );
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function putAction() {
        $this->getResponse()->setBody('resource updated' . $this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function deleteAction() {
        $this->getResponse()->setBody('resource deleted' . $this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(200);
    }

}
