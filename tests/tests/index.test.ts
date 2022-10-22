import client from "../src/client"
import {gql} from "@apollo/client/core";

function sum(a: number, b: number) {
    return a + b;
}

test('adds 1 + 2 to equal 3', () => {
    expect(sum(1, 2)).toBe(3);
    expect(sum(1, 2)).not.toBe(1);
});

test('graphql', async () => {
    const result = await client.query({
        query: gql`
            query {
                productGetProducts{
                    uuid
                    name
                    price
                    priceVat
                }
            }
        `,
    });

    expect(result).not.toBeNull();
})